<?php

namespace flapjack\attend\database\Base;

use \Exception;
use \PDO;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;
use flapjack\attend\database\Schedule as ChildSchedule;
use flapjack\attend\database\ScheduleQuery as ChildScheduleQuery;
use flapjack\attend\database\Map\ScheduleTableMap;

/**
 * Base class that represents a query for the `schedules` table.
 *
 * Table indicating when students are scheduled to attend
 *
 * @method     ChildScheduleQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildScheduleQuery orderByStudentId($order = Criteria::ASC) Order by the student_id column
 * @method     ChildScheduleQuery orderBySchedule($order = Criteria::ASC) Order by the schedule column
 * @method     ChildScheduleQuery orderByStartDate($order = Criteria::ASC) Order by the start_date column
 * @method     ChildScheduleQuery orderByEnteredAt($order = Criteria::ASC) Order by the entered_at column
 *
 * @method     ChildScheduleQuery groupById() Group by the id column
 * @method     ChildScheduleQuery groupByStudentId() Group by the student_id column
 * @method     ChildScheduleQuery groupBySchedule() Group by the schedule column
 * @method     ChildScheduleQuery groupByStartDate() Group by the start_date column
 * @method     ChildScheduleQuery groupByEnteredAt() Group by the entered_at column
 *
 * @method     ChildScheduleQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildScheduleQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildScheduleQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildScheduleQuery leftJoinWith($relation) Adds a LEFT JOIN clause and with to the query
 * @method     ChildScheduleQuery rightJoinWith($relation) Adds a RIGHT JOIN clause and with to the query
 * @method     ChildScheduleQuery innerJoinWith($relation) Adds a INNER JOIN clause and with to the query
 *
 * @method     ChildScheduleQuery leftJoinStudent($relationAlias = null) Adds a LEFT JOIN clause to the query using the Student relation
 * @method     ChildScheduleQuery rightJoinStudent($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Student relation
 * @method     ChildScheduleQuery innerJoinStudent($relationAlias = null) Adds a INNER JOIN clause to the query using the Student relation
 *
 * @method     ChildScheduleQuery joinWithStudent($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the Student relation
 *
 * @method     ChildScheduleQuery leftJoinWithStudent() Adds a LEFT JOIN clause and with to the query using the Student relation
 * @method     ChildScheduleQuery rightJoinWithStudent() Adds a RIGHT JOIN clause and with to the query using the Student relation
 * @method     ChildScheduleQuery innerJoinWithStudent() Adds a INNER JOIN clause and with to the query using the Student relation
 *
 * @method     \flapjack\attend\database\StudentQuery endUse() Finalizes a secondary criteria and merges it with its primary Criteria
 *
 * @method     ChildSchedule|null findOne(?ConnectionInterface $con = null) Return the first ChildSchedule matching the query
 * @method     ChildSchedule findOneOrCreate(?ConnectionInterface $con = null) Return the first ChildSchedule matching the query, or a new ChildSchedule object populated from the query conditions when no match is found
 *
 * @method     ChildSchedule|null findOneById(int $id) Return the first ChildSchedule filtered by the id column
 * @method     ChildSchedule|null findOneByStudentId(int $student_id) Return the first ChildSchedule filtered by the student_id column
 * @method     ChildSchedule|null findOneBySchedule(int $schedule) Return the first ChildSchedule filtered by the schedule column
 * @method     ChildSchedule|null findOneByStartDate(string $start_date) Return the first ChildSchedule filtered by the start_date column
 * @method     ChildSchedule|null findOneByEnteredAt(int $entered_at) Return the first ChildSchedule filtered by the entered_at column
 *
 * @method     ChildSchedule requirePk($key, ?ConnectionInterface $con = null) Return the ChildSchedule by primary key and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSchedule requireOne(?ConnectionInterface $con = null) Return the first ChildSchedule matching the query and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildSchedule requireOneById(int $id) Return the first ChildSchedule filtered by the id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSchedule requireOneByStudentId(int $student_id) Return the first ChildSchedule filtered by the student_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSchedule requireOneBySchedule(int $schedule) Return the first ChildSchedule filtered by the schedule column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSchedule requireOneByStartDate(string $start_date) Return the first ChildSchedule filtered by the start_date column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSchedule requireOneByEnteredAt(int $entered_at) Return the first ChildSchedule filtered by the entered_at column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildSchedule[]|Collection find(?ConnectionInterface $con = null) Return ChildSchedule objects based on current ModelCriteria
 * @psalm-method Collection&\Traversable<ChildSchedule> find(?ConnectionInterface $con = null) Return ChildSchedule objects based on current ModelCriteria
 *
 * @method     ChildSchedule[]|Collection findById(int|array<int> $id) Return ChildSchedule objects filtered by the id column
 * @psalm-method Collection&\Traversable<ChildSchedule> findById(int|array<int> $id) Return ChildSchedule objects filtered by the id column
 * @method     ChildSchedule[]|Collection findByStudentId(int|array<int> $student_id) Return ChildSchedule objects filtered by the student_id column
 * @psalm-method Collection&\Traversable<ChildSchedule> findByStudentId(int|array<int> $student_id) Return ChildSchedule objects filtered by the student_id column
 * @method     ChildSchedule[]|Collection findBySchedule(int|array<int> $schedule) Return ChildSchedule objects filtered by the schedule column
 * @psalm-method Collection&\Traversable<ChildSchedule> findBySchedule(int|array<int> $schedule) Return ChildSchedule objects filtered by the schedule column
 * @method     ChildSchedule[]|Collection findByStartDate(string|array<string> $start_date) Return ChildSchedule objects filtered by the start_date column
 * @psalm-method Collection&\Traversable<ChildSchedule> findByStartDate(string|array<string> $start_date) Return ChildSchedule objects filtered by the start_date column
 * @method     ChildSchedule[]|Collection findByEnteredAt(int|array<int> $entered_at) Return ChildSchedule objects filtered by the entered_at column
 * @psalm-method Collection&\Traversable<ChildSchedule> findByEnteredAt(int|array<int> $entered_at) Return ChildSchedule objects filtered by the entered_at column
 *
 * @method     ChildSchedule[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ?ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 * @psalm-method \Propel\Runtime\Util\PropelModelPager&\Traversable<ChildSchedule> paginate($page = 1, $maxPerPage = 10, ?ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 */
abstract class ScheduleQuery extends ModelCriteria
{
    protected $entityNotFoundExceptionClass = '\\Propel\\Runtime\\Exception\\EntityNotFoundException';

    /**
     * Initializes internal state of \flapjack\attend\database\Base\ScheduleQuery object.
     *
     * @param string $dbName The database name
     * @param string $modelName The phpName of a model, e.g. 'Book'
     * @param string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'attend', $modelName = '\\flapjack\\attend\\database\\Schedule', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildScheduleQuery object.
     *
     * @param string $modelAlias The alias of a model in the query
     * @param Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildScheduleQuery
     */
    public static function create(?string $modelAlias = null, ?Criteria $criteria = null): Criteria
    {
        if ($criteria instanceof ChildScheduleQuery) {
            return $criteria;
        }
        $query = new ChildScheduleQuery();
        if (null !== $modelAlias) {
            $query->setModelAlias($modelAlias);
        }
        if ($criteria instanceof Criteria) {
            $query->mergeWith($criteria);
        }

        return $query;
    }

    /**
     * Find object by primary key.
     * Propel uses the instance pool to skip the database if the object exists.
     * Go fast if the query is untouched.
     *
     * <code>
     * $obj  = $c->findPk(12, $con);
     * </code>
     *
     * @param mixed $key Primary key to use for the query
     * @param ConnectionInterface $con an optional connection object
     *
     * @return ChildSchedule|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ?ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(ScheduleTableMap::DATABASE_NAME);
        }

        $this->basePreSelect($con);

        if (
            $this->formatter || $this->modelAlias || $this->with || $this->select
            || $this->selectColumns || $this->asColumns || $this->selectModifiers
            || $this->map || $this->having || $this->joins
        ) {
            return $this->findPkComplex($key, $con);
        }

        if ((null !== ($obj = ScheduleTableMap::getInstanceFromPool(null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key)))) {
            // the object is already in the instance pool
            return $obj;
        }

        return $this->findPkSimple($key, $con);
    }

    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param mixed $key Primary key to use for the query
     * @param ConnectionInterface $con A connection object
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildSchedule A model object, or null if the key is not found
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT id, student_id, schedule, start_date, entered_at FROM schedules WHERE id = :p0';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), 0, $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
            /** @var ChildSchedule $obj */
            $obj = new ChildSchedule();
            $obj->hydrate($row);
            ScheduleTableMap::addInstanceToPool($obj, null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key);
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param mixed $key Primary key to use for the query
     * @param ConnectionInterface $con A connection object
     *
     * @return ChildSchedule|array|mixed the result, formatted by the current formatter
     */
    protected function findPkComplex($key, ConnectionInterface $con)
    {
        // As the query uses a PK condition, no limit(1) is necessary.
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKey($key)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->formatOne($dataFetcher);
    }

    /**
     * Find objects by primary key
     * <code>
     * $objs = $c->findPks(array(12, 56, 832), $con);
     * </code>
     * @param array $keys Primary keys to use for the query
     * @param ConnectionInterface $con an optional connection object
     *
     * @return Collection|array|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, ?ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getReadConnection($this->getDbName());
        }
        $this->basePreSelect($con);
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKeys($keys)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->format($dataFetcher);
    }

    /**
     * Filter the query by primary key
     *
     * @param mixed $key Primary key to use for the query
     *
     * @return $this The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        $this->addUsingAlias(ScheduleTableMap::COL_ID, $key, Criteria::EQUAL);

        return $this;
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param array|int $keys The list of primary key to use for the query
     *
     * @return $this The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        $this->addUsingAlias(ScheduleTableMap::COL_ID, $keys, Criteria::IN);

        return $this;
    }

    /**
     * Filter the query on the id column
     *
     * Example usage:
     * <code>
     * $query->filterById(1234); // WHERE id = 1234
     * $query->filterById(array(12, 34)); // WHERE id IN (12, 34)
     * $query->filterById(array('min' => 12)); // WHERE id > 12
     * </code>
     *
     * @param mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param string|null $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this The current query, for fluid interface
     */
    public function filterById($id = null, ?string $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(ScheduleTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(ScheduleTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        $this->addUsingAlias(ScheduleTableMap::COL_ID, $id, $comparison);

        return $this;
    }

    /**
     * Filter the query on the student_id column
     *
     * Example usage:
     * <code>
     * $query->filterByStudentId(1234); // WHERE student_id = 1234
     * $query->filterByStudentId(array(12, 34)); // WHERE student_id IN (12, 34)
     * $query->filterByStudentId(array('min' => 12)); // WHERE student_id > 12
     * </code>
     *
     * @see       filterByStudent()
     *
     * @param mixed $studentId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param string|null $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this The current query, for fluid interface
     */
    public function filterByStudentId($studentId = null, ?string $comparison = null)
    {
        if (is_array($studentId)) {
            $useMinMax = false;
            if (isset($studentId['min'])) {
                $this->addUsingAlias(ScheduleTableMap::COL_STUDENT_ID, $studentId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($studentId['max'])) {
                $this->addUsingAlias(ScheduleTableMap::COL_STUDENT_ID, $studentId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        $this->addUsingAlias(ScheduleTableMap::COL_STUDENT_ID, $studentId, $comparison);

        return $this;
    }

    /**
     * Filter the query on the schedule column
     *
     * Example usage:
     * <code>
     * $query->filterBySchedule(1234); // WHERE schedule = 1234
     * $query->filterBySchedule(array(12, 34)); // WHERE schedule IN (12, 34)
     * $query->filterBySchedule(array('min' => 12)); // WHERE schedule > 12
     * </code>
     *
     * @param mixed $schedule The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param string|null $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this The current query, for fluid interface
     */
    public function filterBySchedule($schedule = null, ?string $comparison = null)
    {
        if (is_array($schedule)) {
            $useMinMax = false;
            if (isset($schedule['min'])) {
                $this->addUsingAlias(ScheduleTableMap::COL_SCHEDULE, $schedule['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($schedule['max'])) {
                $this->addUsingAlias(ScheduleTableMap::COL_SCHEDULE, $schedule['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        $this->addUsingAlias(ScheduleTableMap::COL_SCHEDULE, $schedule, $comparison);

        return $this;
    }

    /**
     * Filter the query on the start_date column
     *
     * Example usage:
     * <code>
     * $query->filterByStartDate('2011-03-14'); // WHERE start_date = '2011-03-14'
     * $query->filterByStartDate('now'); // WHERE start_date = '2011-03-14'
     * $query->filterByStartDate(array('max' => 'yesterday')); // WHERE start_date > '2011-03-13'
     * </code>
     *
     * @param mixed $startDate The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param string|null $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this The current query, for fluid interface
     */
    public function filterByStartDate($startDate = null, ?string $comparison = null)
    {
        if (is_array($startDate)) {
            $useMinMax = false;
            if (isset($startDate['min'])) {
                $this->addUsingAlias(ScheduleTableMap::COL_START_DATE, $startDate['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($startDate['max'])) {
                $this->addUsingAlias(ScheduleTableMap::COL_START_DATE, $startDate['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        $this->addUsingAlias(ScheduleTableMap::COL_START_DATE, $startDate, $comparison);

        return $this;
    }

    /**
     * Filter the query on the entered_at column
     *
     * Example usage:
     * <code>
     * $query->filterByEnteredAt(1234); // WHERE entered_at = 1234
     * $query->filterByEnteredAt(array(12, 34)); // WHERE entered_at IN (12, 34)
     * $query->filterByEnteredAt(array('min' => 12)); // WHERE entered_at > 12
     * </code>
     *
     * @param mixed $enteredAt The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param string|null $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this The current query, for fluid interface
     */
    public function filterByEnteredAt($enteredAt = null, ?string $comparison = null)
    {
        if (is_array($enteredAt)) {
            $useMinMax = false;
            if (isset($enteredAt['min'])) {
                $this->addUsingAlias(ScheduleTableMap::COL_ENTERED_AT, $enteredAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($enteredAt['max'])) {
                $this->addUsingAlias(ScheduleTableMap::COL_ENTERED_AT, $enteredAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        $this->addUsingAlias(ScheduleTableMap::COL_ENTERED_AT, $enteredAt, $comparison);

        return $this;
    }

    /**
     * Filter the query by a related \flapjack\attend\database\Student object
     *
     * @param \flapjack\attend\database\Student|ObjectCollection $student The related object(s) to use as filter
     * @param string|null $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return $this The current query, for fluid interface
     */
    public function filterByStudent($student, ?string $comparison = null)
    {
        if ($student instanceof \flapjack\attend\database\Student) {
            return $this
                ->addUsingAlias(ScheduleTableMap::COL_STUDENT_ID, $student->getId(), $comparison);
        } elseif ($student instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            $this
                ->addUsingAlias(ScheduleTableMap::COL_STUDENT_ID, $student->toKeyValue('PrimaryKey', 'Id'), $comparison);

            return $this;
        } else {
            throw new PropelException('filterByStudent() only accepts arguments of type \flapjack\attend\database\Student or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Student relation
     *
     * @param string|null $relationAlias Optional alias for the relation
     * @param string|null $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this The current query, for fluid interface
     */
    public function joinStudent(?string $relationAlias = null, ?string $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Student');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'Student');
        }

        return $this;
    }

    /**
     * Use the Student relation Student object
     *
     * @see useQuery()
     *
     * @param string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \flapjack\attend\database\StudentQuery A secondary query class using the current class as primary query
     */
    public function useStudentQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinStudent($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Student', '\flapjack\attend\database\StudentQuery');
    }

    /**
     * Use the Student relation Student object
     *
     * @param callable(\flapjack\attend\database\StudentQuery):\flapjack\attend\database\StudentQuery $callable A function working on the related query
     *
     * @param string|null $relationAlias optional alias for the relation
     *
     * @param string|null $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this
     */
    public function withStudentQuery(
        callable $callable,
        string $relationAlias = null,
        ?string $joinType = Criteria::INNER_JOIN
    ) {
        $relatedQuery = $this->useStudentQuery(
            $relationAlias,
            $joinType
        );
        $callable($relatedQuery);
        $relatedQuery->endUse();

        return $this;
    }

    /**
     * Use the relation to Student table for an EXISTS query.
     *
     * @see \Propel\Runtime\ActiveQuery\ModelCriteria::useExistsQuery()
     *
     * @param string|null $modelAlias sets an alias for the nested query
     * @param string|null $queryClass Allows to use a custom query class for the exists query, like ExtendedBookQuery::class
     * @param string $typeOfExists Either ExistsQueryCriterion::TYPE_EXISTS or ExistsQueryCriterion::TYPE_NOT_EXISTS
     *
     * @return \flapjack\attend\database\StudentQuery The inner query object of the EXISTS statement
     */
    public function useStudentExistsQuery($modelAlias = null, $queryClass = null, $typeOfExists = 'EXISTS')
    {
        /** @var $q \flapjack\attend\database\StudentQuery */
        $q = $this->useExistsQuery('Student', $modelAlias, $queryClass, $typeOfExists);
        return $q;
    }

    /**
     * Use the relation to Student table for a NOT EXISTS query.
     *
     * @see useStudentExistsQuery()
     *
     * @param string|null $modelAlias sets an alias for the nested query
     * @param string|null $queryClass Allows to use a custom query class for the exists query, like ExtendedBookQuery::class
     *
     * @return \flapjack\attend\database\StudentQuery The inner query object of the NOT EXISTS statement
     */
    public function useStudentNotExistsQuery($modelAlias = null, $queryClass = null)
    {
        /** @var $q \flapjack\attend\database\StudentQuery */
        $q = $this->useExistsQuery('Student', $modelAlias, $queryClass, 'NOT EXISTS');
        return $q;
    }

    /**
     * Use the relation to Student table for an IN query.
     *
     * @see \Propel\Runtime\ActiveQuery\ModelCriteria::useInQuery()
     *
     * @param string|null $modelAlias sets an alias for the nested query
     * @param string|null $queryClass Allows to use a custom query class for the IN query, like ExtendedBookQuery::class
     * @param string $typeOfIn Criteria::IN or Criteria::NOT_IN
     *
     * @return \flapjack\attend\database\StudentQuery The inner query object of the IN statement
     */
    public function useInStudentQuery($modelAlias = null, $queryClass = null, $typeOfIn = 'IN')
    {
        /** @var $q \flapjack\attend\database\StudentQuery */
        $q = $this->useInQuery('Student', $modelAlias, $queryClass, $typeOfIn);
        return $q;
    }

    /**
     * Use the relation to Student table for a NOT IN query.
     *
     * @see useStudentInQuery()
     *
     * @param string|null $modelAlias sets an alias for the nested query
     * @param string|null $queryClass Allows to use a custom query class for the NOT IN query, like ExtendedBookQuery::class
     *
     * @return \flapjack\attend\database\StudentQuery The inner query object of the NOT IN statement
     */
    public function useNotInStudentQuery($modelAlias = null, $queryClass = null)
    {
        /** @var $q \flapjack\attend\database\StudentQuery */
        $q = $this->useInQuery('Student', $modelAlias, $queryClass, 'NOT IN');
        return $q;
    }

    /**
     * Exclude object from result
     *
     * @param ChildSchedule $schedule Object to remove from the list of results
     *
     * @return $this The current query, for fluid interface
     */
    public function prune($schedule = null)
    {
        if ($schedule) {
            $this->addUsingAlias(ScheduleTableMap::COL_ID, $schedule->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the schedules table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(?ConnectionInterface $con = null): int
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(ScheduleTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            ScheduleTableMap::clearInstancePool();
            ScheduleTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

    /**
     * Performs a DELETE on the database based on the current ModelCriteria
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                         if supported by native driver or if emulated using Propel.
     * @throws \Propel\Runtime\Exception\PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public function delete(?ConnectionInterface $con = null): int
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(ScheduleTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(ScheduleTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            ScheduleTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            ScheduleTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

}
